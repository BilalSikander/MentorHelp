import BlockInspector from "./Inspector";
import { __ } from "@wordpress/i18n";
import { Fragment } from "@wordpress/element";
import Categories from "./Categories";

export default ({
	attributes,
	setAttributes,
	className,
	isSelected
}) => {

	return (
		<Fragment>
			<BlockInspector
				{...{ attributes, setAttributes, className, isSelected }}
			/>
			<Categories {...{ attributes, setAttributes }}/>
		</Fragment>
	);
};
