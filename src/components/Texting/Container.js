import React from "react";

import { withRouter, Link } from "react-router-dom";
import { connect } from "react-redux";
import { trim } from "lodash-es";

import TopBar from "../TopBar";
import Footer from "../Footer";

import { redirectNotLogged } from "../../services/User";

import MessageEditor from "./MessageEditor";
import ReferenceProfileData from "./ReferenceProfileData";
import DeliveryLog from "./DeliveryLog";
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
          <div className="btn-group-vertical btn-toolbar">
            <Link to={{hash:"#log"}} className="btn btn-default btn-xs p-1 m-1"
                  title="Delivery log" replace>
              <small><i className="fa fa-bar-chart"></i></small>
            </Link>
            <Link to={{hash:"#messages"}} className="btn btn-default btn-xs p-1 m-1"
                  title="Browse composed messages" replace>
              <small><i className="fa fa-envelope"></i></small>
            </Link>
            <Link to={{hash:"#user"}} className="btn btn-default btn-xs p-1 m-1"
                  title="User messages" replace>
              <small><i className="fa fa-user"></i></small>
            </Link>
          </div>
        </div>
        <DeliveryLog load={props.loadDeliveryStats}
                     log={props.deliveryLog} />
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    logged: state.user.logged,
    refProf: state.texting.profile,
    searchList: state.texting.searchList,
    message: state.texting.message,
    loadDeliveryStats: state.texting.loadDeliveryStats,
    deliveryLog: state.texting.deliveryLog
  }
}

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
