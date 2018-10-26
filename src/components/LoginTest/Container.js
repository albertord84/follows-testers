import React from "react";

import { withRouter } from "react-router-dom";
import { connect } from "react-redux";

import TopBar from "../TopBar";
import Footer from "../Footer";

import {
  loadLoginTestState, lastLoginTestLog, saveLoginTestData$,
  refreshLoginTestLog,
  execLoginNow
} from "../../services/LoginTester";
import { redirectNotLogged } from "../../services/User";

import LogBox from "./LogBox";
import LoginBox from "./LoginBox";
import IntervalBox from "./IntervalBox";

class Container extends React.Component {
  componentWillMount() {
    redirectNotLogged(this.props.logged);
  }
  componentDidMount() {
    loadLoginTestState();
    lastLoginTestLog();
  }
  render() {
    const props = this.props;
    return (
      <div>
        <TopBar/>
        <div className="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
          <h1 className="display-4">Login Test</h1>
          <p className="lead"></p>
        </div>
        <div className="container">
          <form onSubmit={saveLoginTestData$}>
            <fieldset disabled={props.waiting}>
              <div className="row justify-content-center">
                <div className="col-12 mb-3 text-center">
                  <div className="row justify-content-center">
                    <div className="col-5">
                      <LoginBox activated={props.activated}
                                userName={props.userName}
                                password={props.password} />
                    </div>
                    <div className="col-5">
                      <IntervalBox interval={props.interval}
                                   execImmediatedly={execLoginNow} / >
                    </div>
                    <div className="col-10">
                      <LogBox logLines={props.logLines} refreshHandler={refreshLoginTestLog} />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
          </form>
          <Footer />
        </div>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    logged: state.user.logged,
    waiting: state.loginTest.waiting,
    userName: state.loginTest.userName,
    password: state.loginTest.password,
    interval: state.loginTest.interval,
    activated: state.loginTest.activated,
    error: state.loginTest.error,
    logLines: state.loginTest.logLines
  }
}

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
