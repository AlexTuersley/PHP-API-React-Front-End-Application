import React from 'react';

class Home extends React.Component{
       
  render() {
    return (
      <div>
          <div style={{marginTop:"10px"}}className="item">
          <p style={{fontWeight:"bold",fontSize:"30px"}}>Welcome to the CHI 2018</p>    
            <p>For information about the Schedule click the Schedule tab in the menu</p>
            <p>To find a specific author and their content click the authors tab</p>
            <p>For Admin access and other features click the admin tab and complete the login form</p>
            <p>This Website is University Coursework and in no way associated with the CHI Conference or any of its sponsers</p>
          </div>
          
      </div>
    );
  }
}

export default Home;