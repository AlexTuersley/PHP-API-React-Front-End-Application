import React from 'react'

class ContentAuthor extends React.Component{
    state = {
        data:[]
    }
    componentDidMount(){
        const url = "http://localhost/WebAssignment/part1/api/authors/content/" + this.props.contentId
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
    render(){
        let authorList = "";
        this.state.data.map((details, i) => (
             authorList += details.authorName +" "
        ))
        return(
            <p>Authors: {authorList}</p>
        );
    }

}
export default ContentAuthor;