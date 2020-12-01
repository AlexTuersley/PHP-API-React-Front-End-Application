import React from 'react';

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
                <div key ={i}>
                    <p>Title: {details.title} Session: {details.sessionname} Type: {details.sessiontype} Room: {details.room}</p>
                    <p>Day: {details.dayString} Start: {details.startHour}:{details.startMinute} End: {details.endHour}:{details.endMinute}</p>
                    <p>Abstract: {details.abstract}</p>
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