import React from "react";

import { withRouter } from "react-router-dom";
import { connect } from "react-redux";

import TopBar from "../TopBar";
import Footer from "../Footer";
import ServerSelector from "./ServerSelector";
import ClientSelector from "./ClientSelector";
import StatsPanel from "./StatsPanel";

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
                        <div className="col-6">
                            <ServerSelector />
                        </div>
                        <div className="col-6">
                            <ClientSelector />
                        </div>
                        <div className="col-12">
                            <StatsPanel />
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

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
