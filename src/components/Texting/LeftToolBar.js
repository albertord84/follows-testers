import React from 'react';
import { onToolBarButtonClick$ } from '../../services/Texting';

const LeftToolBar = (props) => {
  return (
    <div className="tools fixed-top d-flex align-items-center justify-content-center">
      <div className="btn-group-vertical btn-toolbar">
        <button className="btn btn-default btn-xs p-1 text-secondary delivery-log"
                onClick={(ev) => onToolBarButtonClick$.next(ev.target)}>
          <small><i className="fa fa-bar-chart"></i></small>
        </button>
        <button className="btn btn-default btn-xs p-1 text-secondary active-messages"
                onClick={(ev) => onToolBarButtonClick$.next(ev.target)}>
          <small><i className="fa fa-envelope"></i></small>
        </button>
        <button className="btn btn-default btn-xs p-1 text-secondary inactive-messages"
                onClick={(ev) => onToolBarButtonClick$.next(ev.target)}>
          <small><i className="fa fa-envelope-open-o"></i></small>
        </button>
      </div>
    </div>
  );
};

export default LeftToolBar;