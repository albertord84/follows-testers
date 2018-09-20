import React from 'react';
import { Link } from "react-router-dom";

const LeftToolBar = (props) => {
  return (
    <div className="tools fixed-top d-flex align-items-center">
      <div className="btn-group-vertical btn-toolbar">
        <Link to={{hash:"#log"}} className="btn btn-default btn-xs p-1 m-1 text-secondary"
              title="Delivery log" replace>
          <small><i className="fa fa-bar-chart"></i></small>
        </Link>
        <Link to={{hash:"#messages"}} className="btn btn-default btn-xs p-1 m-1 text-secondary"
              title="Browse composed messages" replace>
          <small><i className="fa fa-envelope"></i></small>
        </Link>
        <Link to={{hash:"#user"}} className="btn btn-default btn-xs p-1 m-1 text-secondary"
              title="User messages" replace>
          <small><i className="fa fa-user"></i></small>
        </Link>
      </div>
    </div>
  );
};

export default LeftToolBar;