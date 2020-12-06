import React from 'react'
import Login from './Login'
import Update from './Update'
/**
 * Uses mutiple components to create a login page and if login is successful an update page, making api calls for both functions
 * 
 * @author Alex Tuersley
 */
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
    const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/login"
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
    const url = "http://unn-w17018264.newnumyspace.co.uk/KF6012/part1/api/update"
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
              <div style={{marginTop:"10px"}}><button onClick={this.handleLogoutClick}>Log out</button></div>
                <Update handleUpdateClick={this.handleUpdateClick} />             
            </div>
    }

    return (
      <div>
        {page}
      </div>
    );
  }
}

export default Admin;