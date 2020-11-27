import React from 'react';

/**
* A text input for searching using a string 
*
* @author Alex Tuersley
*/
class Search extends React.Component {
 render() {
   return (
     <div>
       <label><p>Search: {this.props.query}</p>
       <input
         type='text' 
         placeholder='search'
         value={this.props.query}
         onChange={this.props.handleSearch}
       />
       </label>
     </div>
   )
 }       
}

export default Search;