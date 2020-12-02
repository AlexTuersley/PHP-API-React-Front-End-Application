import React from 'react';
import ContentAuthor from './ContentAuthor';
import { FaAward,FaNewspaper } from "react-icons/fa";
/**
 * Gets all content within a session and displays the information about each individual content
 * 
 * @author Alex Tuersley
 */
class SessionContent extends React.Component {
    state = {
       display:false,
       data:[],
       page:1,
       pageSize:2
    }

    loadSessionContentDetails = () => {
        const url = "http://localhost/WebAssignment/part1/api/content/session/" + this.props.details.sessionId
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
    handleContentClick = (e) => {
        this.setState({display:!this.state.display})
        this.loadSessionContentDetails()
    }

    handlePreviousClick = () => {
      this.setState({page:this.state.page-1})
    }
   
    handleNextClick = () => {
      this.setState({page:this.state.page+1})
    }
     
    render() {
      let sessioncontent = "";
      let buttons = "";
      if (this.state.display) {
        if(this.state.data.length > 0){
          let noOfPages = Math.ceil(this.state.data.length/this.state.pageSize);
          if (noOfPages === 0) {noOfPages=1}
          let disabledPrevious = (this.state.page <= 1);
          let disabledNext = (this.state.page >= noOfPages);
          sessioncontent = this.state.data
          .slice(((this.state.pageSize*this.state.page)-this.state.pageSize),(this.state.pageSize*this.state.page))
          .map((details, i) => (
            <div key={i}>
                <div className="AuthorInfo flex-item"  value={details.contentId}>
                  <p><span>Title: </span>{details.title} {details.award ==="HONORABLE_MENTION" ? <span title="Honourable Mention"><FaNewspaper/></span> : ""}
                  {details.award ==="BEST_PAPER" ? <span title="Best Paper"><FaAward/></span> : ""}</p> 
                  {details.abstract !== "" ? <p><span>Abstract:</span> {details.abstract}</p> : ""}
                  <ContentAuthor contentId={details.contentId}></ContentAuthor>
                </div>
            </div>
          ));
          if(this.state.data.length > this.state.pageSize){
            buttons =  <div><button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
            Page {this.state.page} of {noOfPages}
            <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button></div>;
          }
        
        }
      }
      
      return (
          <div>
            <h5 onClick={this.handleContentClick}><span>Session: {this.props.details.sessionname}</span> <span>Room: {this.props.details.room}</span> <span>Type: {this.props.details.type} </span> {this.props.details.chair !== null?<span>Chair: {this.props.details.chair}</span>:""}</h5>
            {sessioncontent}
            {buttons}
          </div>         
      );
    }  
}
export default SessionContent;