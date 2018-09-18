import React from "react";

import { connect } from "react-redux";
import { withRouter } from "react-router-dom";

import LoginError from "./LoginError";

import {
  submitUserLogin$, loginFormInput$, redirectHomeLogged
} from "../../services/User";

class Login extends React.Component {
  incomingData(ev) {
    loginFormInput$(ev.target);
  }
  onSubmit(ev) {
    ev.preventDefault();
    submitUserLogin$(ev.target);
  }
  componentWillMount() {
    redirectHomeLogged(this.props.logged);
  }
  render() {
    const props = this.props;
    return (
      <div className="container text-center mt-5">
        <form className="form-signin"
              onSubmit={this.onSubmit}>
          <h1 className="h3 mb-3 font-weight-normal mb-4 text-secondary">DUMBU Tester</h1>
          <label htmlFor="userName" className="sr-only">Email address</label>
          <input type="text" id="userName" name="userName"
            className="form-control" placeholder="Email address"
            required autoFocus="on" autoComplete="off"
            disabled={props.logging} onInput={this.incomingData} />
          <label htmlFor="inputPassword" className="sr-only">Password</label>
          <input type="password" id="password" name="password"
            className="form-control" placeholder="Password"
            required disabled={props.logging} onInput={this.incomingData} />
          <button className="btn btn-lg btn-primary btn-block mt-4"
                  type="submit">Sign in</button>
          <LoginError error={props.loginError} />
          <p className="mt-5 mb-3 text-muted">© 2018</p>
        </form>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    logged: state.user.logged,
    logging: state.user.logging,
    loginError: state.user.loginError
  }
}

const connected = connect(mapStateToProps)(Login);

export default withRouter(connected);
