import React from 'react';

class UpdateItem extends React.Component {

  state = {sessionname: this.props.details.sessionname, display:false}

  handleSessionChange = (e) => {
  this.setState({sessionname:e.target.value})
  }
  handleSessionClick = () => {
    this.setState({display:!this.state.display})
  }
  handleSessionNameUpdate = () => {
    this.props.handleUpdateClick(this.props.details.sessionId, this.state.sessionname)
  }

  render() {
      let sessionInfo = "";
    if(this.state.display){
        sessionInfo = <div>
            <label>Name: <input value={this.state.sessionname} size="30" onChange={this.handleSessionChange} /></label>
            <p>Room: {this.props.details.room} Day: {this.props.details.dayString}</p>
            <p>Time: {this.props.details.startHour}:{this.props.details.startMinute}{this.props.details.startMinute === "0" ? "0":""}-{this.props.details.endHour}:{this.props.details.endMinute}{this.props.details.endMinute === "0" ? "0":""}</p>
            <button onClick={this.handleSessionNameUpdate}>Update</button>
        </div>;
    }
    return (
      <div>
        <h2 onClick={this.handleSessionClick}>{this.props.details.sessionname}</h2>
        {sessionInfo}
      </div>
    );
  }
}

export default UpdateItem;