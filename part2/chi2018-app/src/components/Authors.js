import React from 'react';
import Search from './Search';
import Author from './Author';

class Authors extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
          page:1,
          pageSize:10,
          query: "",
          data:[]
        }
        this.handleSearch = this.handleSearch.bind(this);
      }
    componentDidMount() {
        const url = "http://localhost/WebAssignment/part1/api/authors"
            fetch(url)
                .then( (response) => response.json())
                .then((data) => {
                this.setState({data:data.data})
            })
            .catch ((err) => {
                console.log("something went wrong ", err)
            }
            );
        }

        handleMoreClick = () => {
            this.setState({page:this.state.page+1})
        }
        
        handlePreviousClick = () => {
        this.setState({page:this.state.page-1})
        }
        
        handleNextClick = () => {
        this.setState({page:this.state.page+1})
        }
        
        handleSearch = (e) => {
        this.setState({page:1,query:e.target.value})
        }
        
        searchString = (s) => {
        return s.toLowerCase().includes(this.state.query.toLowerCase())
        }
        
        searchDetails = (details) => {
        return ((this.searchString(details.authorName) || this.searchString(details.authorInst)))
        }
        
        render() {
            let filteredData =  (
                this.state.data.filter(this.searchDetails)
            )
     
            let noOfPages = Math.ceil(this.state.data.length/this.state.pageSize)
            if (noOfPages === 0) {noOfPages=1}
            let disabledPrevious = (this.state.page <= 1)
            let disabledNext = (this.state.page >= noOfPages)
              
            return (
              <div>
                <h1>Authors</h1>
                <Search query={this.state.query} handleSearch={this.handleSearch}/>
                { 
                   filteredData
                   .slice(((this.state.pageSize*this.state.page)-this.state.pageSize),(this.state.pageSize*this.state.page))
                   .map((details, i) => (
                     <div key={i}>
                        <Author  details={details}></Author>
                     </div>
                   ))
                }
                <button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
                    Page {this.state.page} of {noOfPages}
                <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button>
              </div>
            );
          }
         
}
export default Authors;