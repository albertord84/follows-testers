import React from 'react';
import ActiveRadioButton from './ActiveRadioButton';
import { loginTestInput$ } from '../../services/LoginTester';

const LoginBox = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Account</h4>
      </div>
      <div className="card-body">
        <div className="input-group">
          <div className="input-group-prepend">
            <span className="input-group-text">@</span>
          </div>
          <input className="form-control" id="userName"
                 placeholder="Instagram username" required=""
                 type="text" autoComplete="off" defaultValue={props.userName}
                 onInput={(e) => loginTestInput$(e.target)} />
            <ActiveRadioButton activated={props.activated} />
        </div>
        <div className="input-group mt-3">
          <input className="form-control" id="password" placeholder="Password"
                 required="" type="password" defaultValue={props.password}
                 onInput={(e) => loginTestInput$(e.target)} />
        </div>
        <div className="input-group mt-3">
          <button className="btn btn-block btn-primary" type="submit">Save</button>
        </div>
      </div>
    </div>
  )
}

export default LoginBox;