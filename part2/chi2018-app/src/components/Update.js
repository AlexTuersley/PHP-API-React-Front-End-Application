import React from 'react';
import UpdateItem from './UpdateItem'
import Search from './Search'

/**
 * Lists all sessions which are searchable
 * 
 * @author Alex Tuersley
 */
class Update extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
          page:1,
          pageSize:8,
          query: "",
          data:[]
        }
        this.handleSearch = this.handleSearch.bind(this);
    }
    componentDidMount() {
        const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/sessions"
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
        return (this.searchString(details.sessionname))
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
            <Search query={this.state.query} handleSearch={this.handleSearch}/>
            {filteredData.slice(((this.state.pageSize*this.state.page)-this.state.pageSize),(this.state.pageSize*this.state.page))
            .map((details,i) => (
            <UpdateItem key={i} details={details} handleUpdateClick={this.props.handleUpdateClick}/>))}
            <button onClick={this.handlePreviousClick} disabled={disabledPrevious}>Previous</button>
                    Page {this.state.page} of {noOfPages}
            <button onClick={this.handleNextClick} disabled={disabledNext}>Next</button>
          </div>
        );
    }
}

export default Update;