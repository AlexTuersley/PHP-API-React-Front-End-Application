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
              <div key={i}>
                  <div className="AuthorInfo flex-item"  value={details.contentId}>
                    <p><span>Title: </span>{details.title} {details.award ==="HONORABLE_MENTION" ? <span title="Honourable Mention"><FaNewspaper/></span> : ""}
                    {details.award ==="BEST_PAPER" ? <span title="Best Paper"><FaAward/></span> : ""}</p> 
                    {details.abstract !== "" ? <p><span>Abstract:</span> {details.abstract}</p> : ""}
                    <ContentAuthor contentId={details.contentId}></ContentAuthor>
                  </div>
              </div>
            ));
          }
        }
       
        return (
            <div>
              <h5 onClick={this.handleContentClick}><span>Session: {this.props.details.sessionname}</span> <span>Room: {this.props.details.room}</span> <span>Type: {this.props.details.type} </span> {this.props.details.chair !== null?<span>Chair: {this.props.details.chair}</span>:""}</h5>
              {sessioncontent}
            </div>         
        );
      }  
}
export default SessionContent;