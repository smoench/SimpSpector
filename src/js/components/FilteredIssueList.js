import React from "react";
import client from "../services/client";
import IssueList from "./IssueList";

export default class FilteredIssueList extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      data: null
    };
  }

  componentDidMount() {
    client.get('/commit/' + this.props.id).then(response => {
      this.setState({
        data: response.data
      });
    });
  }

  render() {
    if (this.state.data === null) {
      return <span>'loading...'</span>;
    }

    return (
      <IssueList issues={this.state.data.result.issues}/>
    );
  }
}
