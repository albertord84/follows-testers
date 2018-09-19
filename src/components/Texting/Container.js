import React from "react";

import { withRouter } from "react-router-dom";
import { connect } from "react-redux";
import { trim } from "lodash-es";

import TopBar from "../TopBar";
import Footer from "../Footer";

import { redirectNotLogged } from "../../services/User";

import MessageEditor from "./MessageEditor";
import ReferenceProfileData from "./ReferenceProfileData";
import { onSendDirectData$ } from "../../services/Texting";

class Container extends React.Component {
  componentWillMount() {
    redirectNotLogged(this.props.logged);
  }
  componentDidMount() {
  }
  render() {
    const props = this.props;
    const canNotSend = props.refProf === null ||
      trim(props.message) === '';
    return (
      <div>
        <TopBar/>
        <div className="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
          <h1 className="display-4">Direct Texting</h1>
          <p className="lead"></p>
        </div>
        <div className="container">
          <div className="row justify-content-center">
            <div className="col-5">
              <ReferenceProfileData refProf={props.refProf}
                  searchList={props.searchList} />
            </div>
            <div className="col-5">
              <MessageEditor message={props.message} />
            </div>
            <div className="col-10 text-center">
              <button type="button" className="btn btn-lg btn-primary"
                      onClick={(ev) => onSendDirectData$.next()}
                      disabled={canNotSend}>Start Texting Followers</button>
            </div>
          </div>
          <Footer />
        </div>
        <div className="tools fixed-top d-flex align-items-center">
          <div className="row m-auto btn-toolbar text-center" role="toolbar">
            <a className="col-12 border p-1 small rounded mb-1" href="#/texting/users">U</a>
            <a className="col-12 border p-1 small rounded mb-1" href="#/texting/stats">S</a>
            <a className="col-12 border p-1 small rounded" href="#/texting/messages">M</a>
          </div>
        </div>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    logged: state.user.logged,
    refProf: state.texting.profile,
    searchList: state.texting.searchList,
    message: state.texting.message
  }
}

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
