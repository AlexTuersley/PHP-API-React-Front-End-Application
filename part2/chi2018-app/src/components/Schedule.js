import React from 'react';
import Sessions from './Sessions';

class Schedule extends React.Component{
    state = {display:false, data:[]}

    loadScheduleDetails = () => {
        const url = "http://localhost/WebAssignment/part1/api/schedule/times?day=" + this.props.details.dayInt
        fetch(url)
        .then( (response) => response.json() )
        .then( (data) => {
            this.setState({data:data.data})
        })
        .catch ((err) => {
            console.log("something went wrong ", err)
        }
        );
    }
    handleScheduleClick = (e) => {
        this.setState({display:!this.state.display})
        this.loadScheduleDetails()
      }
     
      render() {
        let schedule = ""
        if (this.state.display) {
          schedule = this.state.data.map( (details, i) => (
            <div key={i} value={details.slotId}>
              <p className='times'>Time: {details.startHour}:{details.startMinute}{details.startMinute === "0" ? "0":""}-{details.endHour}:{details.endMinute}{details.endMinute === "0" ? "0":""} Type: {details.type}</p>
              <Sessions key={i} details={details}></Sessions>
            </div>
          ))
        }
     
        return (
          <div>
            <h2 className='day' onClick={this.handleScheduleClick}>{this.props.details.dayString}</h2>
            {schedule}
          </div>
        );
      }  
}
export default Schedule;