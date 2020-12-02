import React from 'react';
import { FaAward,FaNewspaper } from "react-icons/fa";
/**
 * Gets all content associated with an author and displays detailed information about the content
 * 
 * @author Alex Tuersley
 */
class Author extends React.Component{
    
    state = {
        data:[],
        display: false,
        page:1,
        pageSize:2
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

    handlePreviousClick = () => {
        this.setState({page:this.state.page-1})
    }
     
    handleNextClick = () => {
        this.setState({page:this.state.page+1})
    }

    render(){      
        let authorInfo = "";
        let buttons = ""
        if(this.state.display){
            let noOfPages = Math.ceil(this.state.data.length/this.state.pageSize);
            if (noOfPages === 0) {noOfPages=1}
            let disabledPrevious = (this.state.page <= 1);
            let disabledNext = (this.state.page >= noOfPages);
            authorInfo = this.state.data
            .slice(((this.state.pageSize*this.state.page)-this.state.pageSize),(this.state.pageSize*this.state.page))
            .map((details, i) => (
                <div className="AuthorInfo" key ={i}>
                    <p><span>Title: </span>{details.title} {details.award ==="HONORABLE_MENTION" ? <span title="Honourable Mention"><FaNewspaper/></span> : ""}
                {details.award ==="BEST_PAPER" ? <span title="Best Paper"><FaAward/></span> : ""}</p>
                    <p><span>Session: {details.sessionname}</span></p>
                    <p><span>Type: {details.sessiontype}</span> <span>Room: {details.room}</span></p>
                    <p><span>Day: {details.dayString}</span> <span>Time:{details.startHour}:{details.startMinute}{details.startMinute === "0" ? "0":""} -{details.endHour}:{details.endMinute}{details.endMinute === "0" ? "0":""}</span></p>
                    <p><span>Abstract: </span>{details.abstract}</p>
                </div>
            ))
            buttons =  <div><button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
            Page {this.state.page} of {noOfPages}
            <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button></div>;
        }
        return(
            <div>
                <h3 onClick={this.handleAuthorClick}>Author: {this.props.details.authorName} Institution: {this.props.details.authorInst}</h3>
                {authorInfo}
                {buttons}
            </div>
        );
    }

}
export default Author;