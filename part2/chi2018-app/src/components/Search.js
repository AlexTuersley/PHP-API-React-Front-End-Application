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
          <input id="search" type='text' placeholder='search' value={this.props.query} onChange={this.props.handleSearch} />
     </div>
   )
 }       
}

export default Search;