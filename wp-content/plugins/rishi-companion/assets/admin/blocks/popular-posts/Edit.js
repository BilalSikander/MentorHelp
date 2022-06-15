import BlockInspector from "./Inspector";
import { __ } from "@wordpress/i18n";
import { Fragment } from "@wordpress/element";
import PopularPosts from "./popularPosts";

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
			<PopularPosts {...{ attributes, setAttributes }}/>
		</Fragment>
	);
};
