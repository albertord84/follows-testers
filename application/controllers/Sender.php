<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class Sender extends MY_Controller {
    
    /**
    * Invocar desde el cron pasando el camino a este script PHP
    * como primer parametro; el nombre de esta clase en minuscula
    * como segundo parametro; este metodo como tercer parametro;
    * y como cuarto parametro el nombre del archivo JSON que es
    * el mensaje que se procesara, y que se encuentra dentro del
    * directorio var.
    * 
    * @param $msgFile Nombre del archivo del mensaje a procesar.
    */
    public function start($msgFile) {
        $this->load->library('logger');
        $this->load->library('process');
        $this->load->library('instagram');
        $this->load->library('messages');
        set_time_limit(0);
        
        try {
            if ($this->process->is_web_request()) {
                $not_allowed_from_web_msg = "ERROR: Not allowed to run from the browser. " .
                                            "Terminating right now.\n";
                $this->logger->write($not_allowed_from_web_msg, SENDER_LOG);
                die();
            }
            
            if ($this->process->is_running()) {
                $already_running_msg = "ERROR: We are already running. " .
                                       "I will terminate right now.\n";
                $this->logger->write($already_running_msg, SENDER_LOG);
                die();
            }
            
            $data = $this->messages->load_message(DIRECTS_POOL_DIR . "/$msgFile");
            
            if ($data->finished) {
                $no_longer_processed_msg = "INFO: This message is no longer being processed. " .
                                           "Terminating...\n";
                $this->logger->write($no_longer_processed_msg, SENDER_LOG);
                die();
            }
            
            $this->process->create_pid_file();
            $instagram = $this->instagram->login_instagram($data);
            $recipData = $this->instagram->get_next_recipient($instagram, $data);
            $sent = $this->instagram->send($instagram, $recipData->lastProf, $data->message);
            
            if (!$sent) {
                throw new \Exception('Unable to text the recipient account ' .
                                     $recipData->lastProf);
            }
            
            $data->maxId = $recipData->maxId;
            $data->lastProf = $recipData->lastProf;
            $data->rankToken = $recipData->rankToken;
            $this->messages->update($msgFile, $data);
            
            $this->process->remove_pid_file();
            $this->logger->write(
                sprintf(
                    "INFO: The sender %s successfully texted to %s",
                    $data->profileId,
                    $data->lastProf
                ),
                SENDER_LOG
            );
        }
        catch(\Exception $mainEx) {
            // set message as finished to stop processing
            $this->messages->set_message_finished($msgFile);
            // register and shout out the exception
            echo $mainEx->getMessage() . PHP_EOL;
            $this->logger->write(
                "ERROR: " . $mainEx->getMessage(),
                SENDER_LOG
            );
            // remove process id
            $this->process->remove_pid_file();
        }
    }
    
    public function delivery() {
        try {
            $this->load->library('logger');
            $log = $this->logger->delivery_log();
            return $this->success('ok', [
                'log' => $log
            ]);
        }
        catch(\Exception $deliveryEx) {
            return $this->error('Delivery log error: ' . $deliveryEx->getMessage());
        }
    }
        
    public function active_messages($username) {
        $this->load->library('messages');
        try {
            $messages = $this->messages->active($username);
            return $this->success('ok', [ 'messages' => $messages ]);
        }
        catch(\Exception $msgListEx) {
            return $this->error("Unable to list $username messages: " . $msgListEx->getMessage());
        }
    }

    public function inactive_messages($username) {
        $this->load->library('messages');
        try {
            $user_message_filenames = $this->messages->only_user_msg_files($username);
            $messages = $this->messages->prepare_message_list(
                $user_message_filenames,
                'msg_filenames_to_objects',
                'inactive_messages_only',
                'add_reference_prof_data',
                'remove_user_creds'
            );
            return $this->success('ok', [ 'messages' => $messages ]);
        }
        catch(\Exception $msgListEx) {
            return $this->error("Unable to list $username messages: " . $msgListEx->getMessage());
        }
    }

}
    