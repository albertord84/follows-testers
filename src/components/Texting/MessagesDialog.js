import React from 'react';
import { onCloseMessagesDialog$ } from '../../services/Texting';
import { map, uniqueId } from "lodash-es";

const Loading = (props) => {
  if (!props.visible) { return ''; }
  return (
    <div className="text-center text-secondary">Loading user messages...</div>
  );
};

const Messages = (props) => {
  if (props.messages.length === 0) { return ''; }
  return map(props.messages, (message) => {
    const id = uniqueId(new Date().getTime());
    return (
      <p key={id} className="mt-0 mb-1 p-0 small">{message.message}</p>
    )
  });
};

const MessagesDialog = (props) => {
  if (!props.load) { return ''; }
  return (
    <div className="delivery-log modal m-auto position-absolute" tabIndex="-1" role="dialog">
      <div className="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div className="modal-content m-auto">
          <div className="modal-header justify-content-center">
            <h4 className="modal-title text-secondary"><b>User Messages</b></h4>
          </div>
          <div className="modal-body">
            <Loading visible={props.messages.length===0} />
            <Messages messages={props.messages} />
          </div>
          <div className="modal-footer d-flex justify-content-center">
            <button type="button" className="btn btn-primary"
                    onClick={() => onCloseMessagesDialog$.next()}>Close</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MessagesDialog;