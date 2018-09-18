import React from "react";
import { withRouter, Link } from "react-router-dom";

class NotFound extends React.Component {
  render() {
    return (
      <div>
        <div className="container">
          <div className="card mt-5 mb-4 box-shadow">
            <div className="card-body text-center">
              <h1 className="text-black-50 display-1 mt-5">Not Found</h1>
              <p className="mt-5"><Link to="/login" className="">Log In</Link></p>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default withRouter(NotFound);
