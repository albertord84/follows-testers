import React from 'react';

const dateList = (dates) => {
    return dates.forEach(date => {
        return (
            <span class="badge badge-secondary m-3">{date}</span>
        );
    });
}

const StatDates = (props) => {
    return (
        <div className="row mt-3">{dateList(props.dates)}</div>
    );
};

export default StatDates;