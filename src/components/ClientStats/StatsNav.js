import React from 'react';
import { pageStatsClick$ } from '../../services/ClientStats';

const StatsNav = (props) => {
    return (
        <div className="stats-nav btn-group btn-toolbar ml-auto float-right">
            <button className="btn btn-default btn-xs p-1 text-secondary less-stats"
                    onClick={(ev) => pageStatsClick$.next(ev.target)}>
                <small><i className="fa fa-minus"></i></small>
            </button>
            <button className="btn btn-default btn-xs p-1 text-secondary more-stats"
                    onClick={(ev) => pageStatsClick$.next(ev.target)}>
                <small><i className="fa fa-plus"></i></small>
            </button>
        </div>
    );
};

export default StatsNav;