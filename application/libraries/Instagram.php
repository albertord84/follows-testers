<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Instagram {
    
    public function login_instagram($data) {
        $instagram = null;
        $instagram = new \InstagramAPI\Instagram(false, true);
        $instagram->login($data->userName, $data->password, SIX_HOURS);
        return $instagram;
    }
    
    public function get_next_recipient($instagram, $data) {
        $rankToken = $data->rankToken === null ?
        \InstagramAPI\Signatures::generateUUID() : $data->rankToken;
        $nextProf = null;
        $followersResponse = $instagram->people
        ->getFollowers($data->profileId, $rankToken, null,
        $data->maxId === null ? null : $data->maxId);
        $list = $followersResponse->getUsers();
        $maxId = $followersResponse->getNextMaxId();
        if ($data->lastProf === '' || $data->lastProf === null) {
            $followers = $followersResponse->getUsers();
            if (count($followers) === 0) {
                throw new \Exception('Selected reference profile has no followers');
            }
            $firstProf = current($followers);
            $nextProf = $firstProf->getPk();
            return (object) [
                'lastProf' => $nextProf,
                'maxId' => $maxId,
                'rankToken' => $rankToken,
            ];
        }
        $nextProf = $this->next_prof_from($list, $data->profileId);
        if (!$nextProf) {
            $maxId = $followersResponse->getNextMaxId();
            $followersResponse = $instagram->people->getFollowers($data->profileId,
            $rankToken, null, $maxId);
            $firstProf = current($followersResponse->getUsers());
            $nextProf = $firstProf->getPk();
        }
        return (object) [
            'lastProf' => $nextProf,
            'maxId' => $maxId,
            'rankToken' => $rankToken,
        ];
    }
    
    public function send($instagram, $prof, $msg) {
        $instagram->direct->sendText([ 'users' => [ $prof ] ], $msg);
        return true;
    }
    
    public function next_prof_from($usersList, $lastProf) {
        $index = 0;
        $nextProf = array_reduce($usersList, function($carry, $user) use ($lastProf, $usersList, &$index) {
            if ($carry === null) {
                $next = array_key_exists($index + 1, $usersList) ?
                $usersList[$index + 1] : null;
                $carry = $user->getPk() === $lastProf ? $next : null;
            }
            $index++;
            return $carry;
        }, null);
        return $nextProf !== null ? $nextProf->getPk() : false;
    }
}