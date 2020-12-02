import React from 'react';
import SessionContent from './SessionContent';
/**
 * Gets sessions within a time slot and passes the information to SessionContent component
 * 
 * @author Alex Tuersley
 */
class Sessions extends React.Component{
        state = {
          display : false,
          data:[]
        }
      
      
      loadSessionDetails() {
        const url = "http://localhost/WebAssignment/part1/api/schedule/" + this.props.details.slotId
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
      handleSessionClick = (e) => {
        this.setState({display:!this.state.display})
        this.loadSessionDetails()
      }  
      render() {  
        let sessioninfo = ''
        if (this.state.display && this.state.data.length > 0) {
          sessioninfo = this.state.data.map( (details, i) => (
            <div key={i} value={details.sessionId}>
              <SessionContent key={i} details={details}></SessionContent>
            </div>
          ))
        }
          return (

            <div>
              <h4 onClick={this.handleSessionClick}>Time: {this.props.details.startHour}:{this.props.details.startMinute}{this.props.details.startMinute === "0" ? "0":""}-{this.props.details.endHour}:{this.props.details.endMinute}{this.props.details.endMinute === "0" ? "0":""} Type: {this.props.details.type}</h4>
              {sessioninfo}
            </div>
          );      
      }

}
export default Sessions;