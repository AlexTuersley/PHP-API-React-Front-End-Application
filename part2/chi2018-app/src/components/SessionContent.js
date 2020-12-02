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
       data:[]
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
     
      render() {
        let sessioncontent = "";
        if (this.state.display) {
          if(this.state.data.length > 0){
            sessioncontent = this.state.data.map((details, i) => (
              <div className="AuthorInfo" key={i} value={details.contentId}>
                <p><span>Title: </span>{details.title} {details.award ==="HONORABLE_MENTION" ? <span title="Honourable Mention"><FaNewspaper/></span> : <span></span>}
                {details.award ==="BEST_PAPER" ? <span title="Best Paper"><FaAward/></span> : <span></span>}</p> 
                {details.abstract !== "" ? <p><span>Abstract:</span> {details.abstract}</p> : <span></span>}
                <ContentAuthor contentId={details.contentId}></ContentAuthor>
              </div>
            ));
          }
          else{
            sessioncontent = <p>There is no Content for this Session</p>;
          }
        }
      
     
        return (
          <div>
            <h5 onClick={this.handleContentClick}>  <p className="session">Session: {this.props.details.sessionname} Room: {this.props.details.room} Type: {this.props.details.type} Chair: {this.props.details.chair}</p></h5>
            {sessioncontent}
          </div>
        );
      }  
}
export default SessionContent;