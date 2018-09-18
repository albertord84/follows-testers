import React from "react";
import RefreshWait from './RefreshWait';

import { uniqueId } from "lodash-es";

const LogLines = (props) => {
  const mapper = (line) => {
    const isInfo = line.indexOf('INFO') !== -1;
    const isError = line.indexOf('ERROR') !== -1;
    const cls = isInfo ? 'text-info' :
      isError ? 'text-danger' : 'text-success';
    const id = uniqueId();
    return (
      <p key={id} className={"small mb-0 " + cls}>{line}</p>
    )
  }
  return props.logLines.map(mapper);
}

const LogBox = (props) => {
  const logLines = props.logLines;
  return (
    <div className="card login-test-log mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Test Execution Log</h4>
        <div className="refresh-log float-right">
          <button className="btn btn-default btn-sm" type="button" onClick={props.refreshHandler}>
            <i className="fa fa-refresh" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <div className="card-body text-left">
        <RefreshWait visible={logLines.length === 0} />
        <LogLines logLines={logLines} />
      </div>
    </div>
  )
}

export default LogBox;
