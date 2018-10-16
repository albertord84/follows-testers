import React from "react";

import { withRouter } from "react-router-dom";
import { connect } from "react-redux";

import TopBar from "../TopBar";
import Footer from "../Footer";
import ServerSelector from "./ServerSelector";
import ClientSelector from "./ClientSelector";
import StatsPanel from "./StatsPanel";

import { redirectNotLogged } from "../../services/User";
import { isLogged, getStatsServer, getStatsClientName, getStatsClientId, getStatsPeriod, getClientStats } from "../../store";

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
                            <ServerSelector server={props.server}
                                            period={props.period} />
                        </div>
                        <div className="col-6">
                            <ClientSelector clientName={props.clientName}
                                            clientId={props.clientId}
                                            server={props.server} />
                        </div>
                        <div className="col-12">
                            <StatsPanel stats={props.stats}
                                        period={props.period} />
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
        logged: isLogged(),
        server: getStatsServer(),
        clientName: getStatsClientName(),
        clientId: getStatsClientId(),
        period: getStatsPeriod(),
        stats: getClientStats()
    }
}

const connected = connect(mapStateToProps)(Container);

export default withRouter(connected);
