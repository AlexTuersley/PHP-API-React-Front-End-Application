import React from 'react'
import Login from './Login'
import Update from './Update'

class Admin extends React.Component {

  constructor(props) {
    super(props);
    this.state = {"authenticated":false, "email":"", "password":""}
 
    this.handleEmail = this.handleEmail.bind(this);
    this.handlePassword = this.handlePassword.bind(this);
  }

componentDidMount() {
  if(localStorage.getItem('myToken')) {
    this.setState({"authenticated":true});
  } 
}

postData = (url, myJSON, callback) => {
  fetch(url, {   method: 'POST',
                 headers : new Headers(),
                 body:JSON.stringify(myJSON)})
    .then( (response) => response.json() )
    .then( (data) => {
      callback(data)
    })
    .catch ((err) => {
      console.log("something went wrong ", err)
    }
  );
}

loginCallback = (data) => {
  console.log(data)
  if (data.status === 200) {
    this.setState({"authenticated":true, "token":data.token})
    localStorage.setItem('myToken', data.token);  
  }
}

updateCallback = (data) => {
  console.log(data)
  if (data.status !== 200) {
    this.setState({"authenticated":false})
    localStorage.removeItem('myToken');  
  }
}

handleLoginClick = () => {
  const url = "http://localhost/WebAssignment/part1/api/login"
  let myJSON = {"email":this.state.email, "password":this.state.password}
  this.postData(url, myJSON, this.loginCallback)
}

handleLogoutClick = () => {
  this.setState({"authenticated":false})
  localStorage.removeItem('myToken'); 
}
handlePassword = (e) => {
  this.setState({password:e.target.value})
}
handleEmail = (e) => {
  this.setState({email:e.target.value})
}

handleUpdateClick = (sessionId, sessionname) => {
  const url = "http://localhost/WebAssignment/part1/api/update"
  console.log(sessionId);
  console.log(sessionname);
  console.log(localStorage.getItem('myToken'));
  if (localStorage.getItem('myToken')) {
    let myToken = localStorage.getItem('myToken')
    let myJSON = {
      "token":myToken,
      "sessionId": sessionId,
      "sessionname":sessionname
     }
     this.postData(url, myJSON, this.updateCallback)
   } else {
     this.setState({"authenticated":false})
   }
} 

render() {
  let page = <Login handleLoginClick={this.handleLoginClick} email={this.state.email} password={this.props.password} handleEmail={this.handleEmail} handlePassword={this.handlePassword}/>
  if (this.state.authenticated) {
    page = <div>
            <button onClick={this.handleLogoutClick}>Log out</button>
            <Update handleUpdateClick={this.handleUpdateClick} />             
           </div>
  }

  return (
    <div>
      <h1>Admin</h1>
      {page}
    </div>
  );
}
}

export default Admin;