import React from 'react';
import { onCloseMessagesDialog$ } from '../../services/Texting';
import { map, uniqueId } from "lodash-es";

const shorten = (str, len = 100) => {
  return str.substring(0, len) + '...';
}

const Loading = (props) => {
  if (!props.visible) { return ''; }
  return (
    <div className="text-center text-secondary">Loading user messages...</div>
  );
};

const Messages = (props) => {
  if (props.messages.length === 0) { return ''; }
  const items = map(props.messages, (message) => {
    const id = uniqueId(new Date().getTime());
    const msgText = shorten(message.message);
    return (      
      <div className="media mb-5" key={id}>
        <img className="prof-photo mr-3 rounded-circle" src={message.profPic} alt="Profile" />
        <div className="media-body small">
          <h5 className="mt-0 mb-2"><b>{message.profName}</b></h5>
          <span className="text-muted">{msgText}...</span>
          <small className="badge badge-secondary float-right">Sent: {message.sent}</small>
        </div>
      </div>
    )
  });
  return (
    <div>{items}</div>
  );
};

const MessagesDialog = (props) => {
  if (!props.load) { return ''; }
  return (
    <div className="messages modal m-auto position-absolute" tabIndex="-1" role="dialog">
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