import React from "react";

const Footer = (props) => {
  return (
    <footer className="pt-4 my-md-5 pt-md-5 border-top">
      <div className="row">
        <div className="col-12 col-md">
          <small className="d-block mb-3 text-muted">© 2018</small>
        </div>
        <div className="col-6 col-md">
          <h5>Features</h5>
          <ul className="list-unstyled text-small">
            <li><a className="text-muted" href="/cool">Cool stuff</a></li>
            <li><a className="text-muted" href="/future">Future features</a></li>
          </ul>
        </div>
        <div className="col-6 col-md">
          <h5>Resources</h5>
          <ul className="list-unstyled text-small">
            <li><a className="text-muted" href="/apis">APIs</a></li>
            <li><a className="text-muted" href="/how">How to...</a></li>
          </ul>
        </div>
        <div className="col-6 col-md">
          <h5>About</h5>
          <ul className="list-unstyled text-small">
            <li><a className="text-muted" href="/team">Team</a></li>
            <li><a className="text-muted" href="/company">Our company</a></li>
            <li><a className="text-muted" href="/privacy">Privacy</a></li>
            <li><a className="text-muted" href="/terms">Terms</a></li>
          </ul>
        </div>
      </div>
    </footer>
  )
}

export default Footer;
