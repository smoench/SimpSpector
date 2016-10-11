import React from "react";
import client from "../services/client";
import IssueList from "./IssueList";
import FilterList from "./FilterList";

export default class FilteredIssueList extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      data: null,
      filter_gadget: null,
      filter_level: null,
    };
  }

  componentDidMount() {
    client.get('/commit/' + this.props.id).then(response => {
      this.setState({
        data: response.data
      });
    });
  }

  filter(key, value) {
    this.setState({
      ['filter_' + key]: value
    })
  }

  render() {
    if (this.state.data === null) {
      return <span>loading...</span>;
    }

    const filter = {
      gadget: this.state.filter_gadget,
      level: this.state.filter_level
    };

    const filteredIssues = filterIssues(this.state.data.result.issues, filter);

    return (
      <div className="ui grid">
        <div className="four wide column">
          <FilterList filters={createFilterView(filteredIssues, filter)} filter={this.filter.bind(this)}/>
        </div>
        <div className="twelve wide column">
          <IssueList issues={filteredIssues}/>
        </div>
      </div>
    );
  }
}

function filterIssues(issues, filters) {
  return issues.filter(issue => {
    if (filters.gadget && issue.gadget !== filters.gadget) {
      return false;
    }

    if (filters.level && issue.level !== filters.level) {
      return false;
    }

    return true;
  });
}


function createFilterView(issues, filter) {
  return [
    {
      key: 'level',
      title: 'Levels',
      choices: createChoices(issues, 'level', filter.level)
    },
    {
      key: 'gadget',
      title: 'Gadgets',
      choices: createChoices(issues, 'gadget', filter.gadget)
    }
  ];
}

function createChoices(issues, filterKey, activeChoice) {
  let choices = new Map();

  for (let issue of issues) {
    const key = issue[filterKey];

    if (!choices.has(key)) {
      choices.set(key, {
        key: key,
        label: key,
        count: 0,
        active: activeChoice === key
      });
    }

    choices.get(key).count++;
  }

  return [...choices.values()];
}
