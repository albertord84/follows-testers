import React from "react";
import { withRouter, Link } from "react-router-dom";
import { connect } from "react-redux";
import TopBar from "./TopBar";
import Footer from "./Footer";
import { redirectNotLogged } from "../services/User";

class Home extends React.Component {
  componentWillMount() {
    redirectNotLogged(this.props.logged);
  }
  render() {
    return (
      <div>
        <TopBar/>
        <div className="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
          <h1 className="display-4">Try the system</h1>
          <p className="lead">Quickly test the system features following the instructions at every section.</p>
        </div>
        <div className="container">
          <div className="card-deck mb-3 text-center">
            <div className="card mb-4 box-shadow">
              <div className="card-header">
                <h4 className="my-0 font-weight-normal">Login Test</h4>
              </div>
              <div className="card-body">
                <ul className="list-unstyled mt-3 mb-4">
                  <li>Set user credentials</li>
                  <li>Set login period</li>
                  <li>Check logs</li>
                </ul>
                <Link className="btn btn-lg btn-block btn-primary" to="/test/login">Start this test</Link>
              </div>
            </div>
            <div className="card mb-4 box-shadow">
              <div className="card-header">
                <h4 className="my-0 font-weight-normal">Followers</h4>
              </div>
              <div className="card-body">
                <ul className="list-unstyled mt-3 mb-4">
                  <li>Select a profile</li>
                  <li>Follow or unfollow</li>
                  <li>Verify followers/following list</li>
                </ul>
                <Link className="btn btn-lg btn-block btn-primary" to="/test/follow">Start this test</Link>
              </div>
            </div>
            <div className="card mb-4 box-shadow">
              <div className="card-header">
                <h4 className="my-0 font-weight-normal">Posts</h4>
              </div>
              <div className="card-body">
                <ul className="list-unstyled mt-3 mb-4">
                  <li>Select profile</li>
                  <li>Browse posts</li><li>Paginate over results</li>
                </ul>
                <button type="button" className="btn btn-lg btn-block btn-primary">Start this test</button>
              </div>
            </div>
            <div className="card mb-4 box-shadow">
              <div className="card-header">
                <h4 className="my-0 font-weight-normal">Direct Texting</h4>
              </div>
              <div className="card-body">
                <ul className="list-unstyled mt-3 mb-4">
                  <li>Select a profile</li>
                  <li>Compose the text</li>
                  <li>Monitor the delivery</li>
                </ul>
                <Link className="btn btn-lg btn-block btn-primary" to="/texting">Start this test</Link>
              </div>
            </div>
          </div>
          <Footer />
        </div>
      </div>
    )
  }
}

const mapStateToProps = (state) => {
  return {
    logged: state.user.logged
  }
}

const connected = connect(mapStateToProps)(Home);

export default withRouter(connected);
