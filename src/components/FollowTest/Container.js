import React from "react";
import { withRouter } from "react-router-dom";
import { connect } from "react-redux";

import TopBar from "../TopBar";
import Footer from "../Footer";

import { redirectNotLogged } from "../../services/User";

import Form from "./Form";

class Container extends React.Component {
  componentWillMount() {
    redirectNotLogged(this.props.logged);
  }
  componentDidMount() {
  }
  render() {
    const props = this.props;
    return (
      <div>
        <TopBar/>
        <div className="follow-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
          <h1 className="display-4">Follow Test</h1>
          <p className="lead"></p>
        </div>
        <div className="container">
          <Form waiting={props.waiting} userName={props.userName}
                password={props.password} users={props.users}
                followingList={props.following} followersList={props.followers} />
          <Footer />
        </div>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    userName: state.followTest.userName,
    password: state.followTest.password,
    waiting: state.followTest.waiting,
    users: state.followTest.users,
    followers: state.followTest.followers,
    following: state.followTest.following,
    logged: state.user.logged
  }
}

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
