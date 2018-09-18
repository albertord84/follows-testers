import React from "react";
import { Router, Route, Switch } from "react-router-dom";
import Login from "./Login/Container";
import Home from "./Home";
import LoginTest from "./LoginTest";
import FollowTest from "./FollowTest";
import NotFound from "./NotFound";
import Texting from "./Texting";

import history from "../history";

class App extends React.Component {
  render() {
    return (
      <Router history={history}>
        <div>
          <Switch>
            <Route exact path="/" component={Login} />
            <Route exact path="/login" component={Login} />
            <Route exact path="/home" component={Home} />
            <Route exact path="/test/login" component={LoginTest} />
            <Route exact path="/test/follow" component={FollowTest} />
            <Route exact path="/texting" component={Texting} />
            <Route component={NotFound} />
          </Switch>
        </div>
      </Router>
    )
  }
}

export default App;
