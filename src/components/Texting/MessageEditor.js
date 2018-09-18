import React from 'react';
import { onDirectMessageTextInput$ } from '../../services/Texting';

const MessageEditor = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Message for Followers</h4>
      </div>
      <div className="card-body">
        <div className="input-group">
          <textarea className="form-control" rows="4"
              onInput={(ev) => onDirectMessageTextInput$.next(ev.target.value)}></textarea>
        </div>
      </div>
    </div>
  );
};

export default MessageEditor;