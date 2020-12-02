import React from 'react';
import Sessions from './Sessions';
/**
 * Displats Schedule days and gets time slots on a selected day, passing the data to Sessions component
 * 
 * @author Alex Tuersley
 */
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