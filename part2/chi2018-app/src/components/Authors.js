import React from 'react';
import Search from './Search';
import Author from './Author';
/**
 * Gets a full list of authors, which are searchable
 * 
 * @author Alex Tuersley
 */
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
      const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/authors"
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
    loadsearchDetails(query) {
      const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/authors?search=" + query
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

      handlePreviousClick = () => {
        this.setState({page:this.state.page-1})
      }
      
      handleNextClick = () => {
        this.setState({page:this.state.page+1})
      }
      
      handleSearch = (e) => {
        this.setState({page:1,query:e.target.value})
        this.loadsearchDetails(e.target.value)
      }

      
      render() {
        let filteredData =  (
          this.state.data
        )
        let noOfPages = Math.ceil(this.state.data.length/this.state.pageSize)
        let disabledPrevious = (this.state.page <= 1)
        let disabledNext = (this.state.page >= noOfPages)
        
        if (noOfPages === 0) {noOfPages=1}
        
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