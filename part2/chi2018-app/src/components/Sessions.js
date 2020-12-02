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
          data:[],
          page:1,
          pageSize:6
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
      handlePreviousClick = () => {
        this.setState({page:this.state.page-1})
      }
     
      handleNextClick = () => {
        this.setState({page:this.state.page+1})
      }
       
      render() {  
        let sessioninfo = ''
        let buttons = ''
        let noOfPages = Math.ceil(this.state.data.length/this.state.pageSize);
        if (noOfPages === 0) {noOfPages=1}
        let disabledPrevious = (this.state.page <= 1);
        let disabledNext = (this.state.page >= noOfPages);
        if (this.state.display && this.state.data.length > 0) {
          sessioninfo = this.state.data
          .slice(((this.state.pageSize*this.state.page)-this.state.pageSize),(this.state.pageSize*this.state.page))
          .map( (details, i) => (
            <div key={i} value={details.sessionId}>
              <SessionContent key={i} details={details}></SessionContent>
            </div>
          ));
          if(this.state.length > this.state.pageSize){
            buttons = <div>
            <button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
                Page {this.state.page} of {noOfPages}
            <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button>
            </div>
          }
        
        }
          return (

            <div key={this.props.details.slotId}>
              <h4 onClick={this.handleSessionClick}>Time: {this.props.details.startHour}:{this.props.details.startMinute}{this.props.details.startMinute === "0" ? "0":""}-{this.props.details.endHour}:{this.props.details.endMinute}{this.props.details.endMinute === "0" ? "0":""} Type: {this.props.details.type}</h4>
              {sessioninfo}
              {buttons}
            </div>
          );      
      }

}
export default Sessions;