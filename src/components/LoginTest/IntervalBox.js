import React from 'react';
import { loginTestInput$ } from '../../services/LoginTester';

const IntervalBox = (props) => {
  return (
    <div className="card exec-interval mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Execution Interval</h4>
        <div className="exec-immediatedly float-right">
          <button className="btn btn-default btn-sm" type="button" onClick={props.execImmediatedly}>
            <i className="fa fa-thumbs-up" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <div className="card-body">
        <div className="input-group">
          <input className="form-control" id="interval" name="interval"
                 placeholder="Time interval in hours" required="" type="text"
                 autoComplete="off" defaultValue={props.interval}
                 onInput={(e) => loginTestInput$(e.target)} />
          <div className="input-group-prepend">
            <button className="btn btn-block btn-primary"
                    type="submit">Set Interval</button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default IntervalBox;
