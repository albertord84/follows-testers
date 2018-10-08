import React from 'react';

const LeftToolBar = (props) => {
  return (
    <div className="tools fixed-top d-flex align-items-center justify-content-center">
      <div className="btn-group-vertical btn-toolbar">
        <button className="btn btn-default btn-xs p-1 text-secondary delivery-log"
              title="Delivery log">
          <small><i className="fa fa-bar-chart"></i></small>
        </button>
        <button className="btn btn-default btn-xs p-1 text-secondary active-messages"
              title="Browse composed messages">
          <small><i className="fa fa-envelope"></i></small>
        </button>
        <button className="btn btn-default btn-xs p-1 text-secondary inactive-messages"
              title="Browse stopped messages">
          <small><i className="fa fa-envelope-open-o"></i></small>
        </button>
      </div>
    </div>
  );
};

export default LeftToolBar;