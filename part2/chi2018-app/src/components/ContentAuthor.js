import React from 'react'
/**
 * Gets a list of authors based on a content id
 * 
 * @author Alex Tuersley
 */
class ContentAuthor extends React.Component{
    state = {
        data:[]
    }
    componentDidMount(){
        const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/authors/content/" + this.props.contentId
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
        let list = "";
        this.state.data.map((details, i) => (
            i !== 0 ? authorList += ", "+ details.authorName : authorList += details.authorName
        ))
        if(authorList !== ""){
            list = <p><span>Authors:</span> {authorList}</p>;
        }
        return(
            <div>
                {list}
            </div>
        );
    }

}
export default ContentAuthor;