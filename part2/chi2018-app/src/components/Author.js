import React from 'react';
import { FaAward,FaNewspaper } from "react-icons/fa";

class Author extends React.Component{
    
    state = {
        data:[],
        display: false
    }
    loadAuthorContent = () =>{
        const url = "http://localhost/WebAssignment/part1/api/authors/" + this.props.details.authorId
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
    handleAuthorClick = (e) => {
        this.setState({display:!this.state.display})
        this.loadAuthorContent();
    }


   
    render(){      
        let authorInfo = "";
        if(this.state.display){
            authorInfo = this.state.data.map((details, i) => (
                <div className="AuthorInfo" key ={i}>
                    <p><span>Title: </span>{details.title} {details.award ==="HONORABLE_MENTION" ? <span title="Honourable Mention"><FaNewspaper/></span> : <span></span>}
                {details.award ==="BEST_PAPER" ? <span title="Best Paper"><FaAward/></span> : <span></span>}</p>
                    <p><span>Session: </span>{details.sessionname}</p>
                    <p> <span>Type: </span>{details.sessiontype} <span>Room: </span>{details.room}</p>
                    <p><span>Day: </span>{details.dayString} <span>Time: </span>{details.startHour}:{details.startMinute}{details.startMinute === "0" ? "0":""} <span>-</span>{details.endHour}:{details.endMinute}{details.endMinute === "0" ? "0":""}</p>
                    <p><span>Abstract: </span>{details.abstract}</p>
                </div>
            ))
        }
        return(
            <div>
                <h3 onClick={this.handleAuthorClick}>Author: {this.props.details.authorName} Institution: {this.props.details.authorInst}</h3>
                {authorInfo}
            </div>
        );
    }

}
export default Author;