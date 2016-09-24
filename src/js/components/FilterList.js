import React from "react";
import Filter from "./Filter";

export default class FilterList extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div>
        {this.props.filters.map((filter) => {
          return <Filter key={filter.key} title={filter.title} filterKey={filter.key} choices={filter.choices} filter={this.props.filter}/>;
        })}
      </div>
    );
  }
}
