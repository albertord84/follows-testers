import React from "react";

import { withRouter } from "react-router-dom";
import { connect } from "react-redux";

import TopBar from "../TopBar";
import Footer from "../Footer";
import ServerSelector from "./ServerSelector";

import { redirectNotLogged } from "../../services/User";

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
        <div className="px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
          <h1 className="display-4">Client Stats</h1>
          <p className="lead"></p>
        </div>
        <div className="container">
          <div className="row justify-content-center">
            <ServerSelector />
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

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
