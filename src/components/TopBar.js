import React from "react";
import { Link } from "react-router-dom";

const TopBar = (props) => {
  return (
    <div className="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
      <h5 className="my-0 mr-md-auto font-weight-normal">DUMBU</h5>
      <nav className="my-2 my-md-0 mr-md-3">
        <Link className="p-2 text-dark" to="/test/login" replace>Login test</Link>
        <Link className="p-2 text-dark" to="/test/follow">Followers</Link>
        <Link className="p-2 text-dark" to="/posts">Posts</Link>
        <Link className="p-2 text-dark" to="/texting">Texting</Link>
      </nav>
      <Link className="btn btn-outline-primary" to="/logout">Logout</Link>
    </div>
  )
}

export default TopBar;
