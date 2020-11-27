import React from 'react';
import ContentAuthor from './ContentAuthor';

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
          sessioncontent = this.state.data.map((details, i) => (
            <div key={i} value={details.contentId}>
              <p onClick={this.handleFurtherContentClick}>Title: {details.title} Award: {details.award}</p>
              <p>Abstract: {details.abstract}</p>
              <ContentAuthor contentId={details.contentId}></ContentAuthor>
            </div>
          ));
        }
      
     
        return (
          <div>
            <h5 onClick={this.handleContentClick}>Content</h5>
            {sessioncontent}
          </div>
        );
      }  
}

export default SessionContent;