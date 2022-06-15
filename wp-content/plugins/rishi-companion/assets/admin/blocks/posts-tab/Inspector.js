import { __ } from "@wordpress/i18n";
import { PanelBody, ToggleControl, TextControl, RangeControl } from "@wordpress/components";
import { InspectorControls } from "@wordpress/block-editor";

export default ({ attributes, setAttributes }) => {
	const {
		postsTabRecentLabel,
        postsTabPopularLabel,
		postsTabCount,
		postsTabShowThumbnail,
		postsTabShowDate
	} = attributes;

	return (
		<InspectorControls key="inspector">
			<PanelBody
				title={__("Settings", "rishi-companion")}
				className={"rishi-panel-label"}
				initialOpen={true}
			>   
                <div className="rishi-blocks-option">                
                    <TextControl
                        label={__("Tab-1 Title", "rishi-companion")}
                        className="posts-tab-recent-title rishi-input-field"
                        value={postsTabRecentLabel}
                        onChange={(postsTabRecentLabel) => setAttributes({ postsTabRecentLabel })}
                    />
                </div>
                <div className="rishi-blocks-option">                
                    <TextControl
                        label={__("Tab-2 Title", "rishi-companion")}
                        className="posts-tab-popular-title rishi-input-field"
                        value={postsTabPopularLabel}
                        onChange={(postsTabPopularLabel) => setAttributes({ postsTabPopularLabel })}
                    />
                </div>
                <div className="rishi-blocks-option">
                    <label>
                        {__("Number of Posts", "rishi-companion")}
                    </label>
                    <RangeControl
                        value={postsTabCount}
                        min={1}
                        step={1}
                        onChange={(newCount) =>
                            setAttributes({
                                postsTabCount: newCount,
                            })
                        }
                    />
                </div>
                <div className="rishi-blocks-option">
                    <ToggleControl
                        label={__("Featured Image", "rishi-companion")}
                        checked={!!postsTabShowThumbnail}
                        onChange={() =>
                            setAttributes({
                                postsTabShowThumbnail: !postsTabShowThumbnail,
                            })
                        }
                    />
                </div>
                <div className="rishi-blocks-option">
                    <ToggleControl
                        label={__("Post Date", "rishi-companion")}
                        checked={!!postsTabShowDate}
                        onChange={() =>
                            setAttributes({
                                postsTabShowDate: !postsTabShowDate,
                            })
                        }
                    />
                </div>
			</PanelBody>
		</InspectorControls>
	);
};