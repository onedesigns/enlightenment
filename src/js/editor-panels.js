import InlineSVG from 'svg-inline-react'
import he from 'he'

const { useSelect, useDispatch } = wp.data
const { useState, useEffect } = wp.element
const { PluginDocumentSettingPanel } = wp.editPost
const { ToggleControl, RadioControl, ColorPicker, __experimentalAlignmentMatrixControl } = wp.components
const AlignmentMatrixControl = __experimentalAlignmentMatrixControl
const { registerPlugin } = wp.plugins

const args = { ...enlightenment_editor_panels_args }

args.header_style.image_position.last_position = args.header_style.image_position.template_position.replace( '-', ' ' )
args.header_style.header_overlay.last_overlay  = args.header_style.header_overlay.template_overlay

const EnlightenmentHeaderStyle = () => {
    const {
        meta,
        meta: {
			_enlightenment_navbar_transparent,
            _enlightenment_single_post_thumbnail,
            _enlightenment_header_image_position,
            _enlightenment_header_overlay_color,
        },
    } = useSelect( ( select ) => ( {
        meta: select('core/editor').getEditedPostAttribute('meta') || {},
    } ) )

    const { editPost } = useDispatch('core/editor')

	const [ navbarTransparent, setNavbarTransparent ] = useState( _enlightenment_navbar_transparent     )
    const [ imageStyle,        setImageStyle        ] = useState( _enlightenment_single_post_thumbnail  )
    const [ imagePosition,     setImagePosition     ] = useState( _enlightenment_header_image_position.replace( '-', ' ' ) )
    const [ overlayColor,      setOverlayColor      ] = useState( _enlightenment_header_overlay_color   )

	const hasImagePosition = ( '' !== imagePosition )
    const hasCustomOverlay = ( '' !== overlayColor )

    useEffect( () => {
        editPost( {
            meta: {
	            ...meta,
				_enlightenment_navbar_transparent:     navbarTransparent,
	            _enlightenment_single_post_thumbnail:  imageStyle,
	            _enlightenment_header_image_position:  imagePosition.replace( ' ', '-' ),
	            _enlightenment_header_overlay_color:   overlayColor,
            },
        } )
    }, [ navbarTransparent, imageStyle, imagePosition, overlayColor ] )

	const navbarTransparentToggle = ( () => {
		if ( args.header_style.post_type != 'page' ) {
			return null
		}

		return (
			<div className="enlightenment-controls enlightenment-controls__navbar-transparent">
				<ToggleControl
					label={ he.decode( args.header_style.navbar_transparent.label ) }
					help={ he.decode( args.header_style.navbar_transparent.description ) }
					checked={ '1' == navbarTransparent }
					onChange={ ( state ) => {
						if ( true === state ) {
							setNavbarTransparent( '1' )
						} else {
							setNavbarTransparent( '' )
						}
					} }
				/>
			</div>
		)
	} )()

    const RadioLabel = ( props ) => {
        const image = props.image ? ( props.image.search( '<svg ' ) == 0 ? <InlineSVG src={props.image} /> : <img src={props.image} alt={props.label} /> ) : null
        const label = props.image ? <span className="screen-reader-text">{ he.decode( props.label ) }</span> : he.decode( props.label )

        return (
            <span className={ 'enlightenment-' + ( props.image ? 'image' : 'text' ) + '-radio__label' }>
                {image}
                {label}
            </span>
        )
    }

    const imageStyleControls = ( () => {
        var className = 'enlightenment-image-radio'

        const options = args.header_style.image_style.options.map( ( option, index ) => {
            if ( option.image ) {
                className += ' enlightenment-image-radio__option-' + index + '-has-image'
            } else {
                className += ' enlightenment-image-radio__option-' + index + '-is-text'
            }

            return ( {
                label: <RadioLabel label={option.label} image={option.image} />,
                value: option.value,
            } )
        } )

        return (
            <RadioControl
                className={ className }
                label={ he.decode( args.header_style.image_style.label ) }
                selected={ imageStyle }
                options={ options }
                onChange={ setImageStyle }
            />
        )
    } )()

    return (
        <PluginDocumentSettingPanel
            name="enlightenment-header-style"
            title={ args.header_style.panel_title }
            className="enlightenment-header-style"
        >
			{ navbarTransparentToggle }

            <div className="enlightenment-controls enlightenment-controls__image-style">
                { imageStyleControls }
            </div>

            <div className="enlightenment-controls enlightenment-controls__image-position">
				<label className="components-base-control__label">{ he.decode( args.header_style.image_position.label ) }</label>

				<ToggleControl
                    label={ he.decode( args.header_style.image_position.toggle ) }
                    checked={ ! hasImagePosition }
                    onChange={ () => {
                        if ( hasImagePosition ) {
                            args.header_style.image_position.last_position = imagePosition
                            setImagePosition( '' )
                        } else {
                            setImagePosition( args.header_style.image_position.last_position )
                        }
                    } }
                />

				<div className={ 'enlightenment-image-position' + ( hasImagePosition ? '' : ' hidden' ) }>
                    <AlignmentMatrixControl
                        value={ imagePosition }
						defaultValue={ args.header_style.image_position.template_position.replace( '-', ' ' ) }
                        onChange={ ( value ) => setImagePosition( value ) }
                    />
                </div>
            </div>

            <div className="enlightenment-controls enlightenment-controls__header-overlay">
                <label className="components-base-control__label">{ he.decode( args.header_style.header_overlay.label ) }</label>

                <ToggleControl
                    label={ he.decode( args.header_style.header_overlay.toggle ) }
                    checked={ ! hasCustomOverlay }
                    onChange={ () => {
                        if ( hasCustomOverlay ) {
                            args.header_style.header_overlay.last_overlay = overlayColor
                            setOverlayColor( '' )
                        } else {
                            setOverlayColor( args.header_style.header_overlay.last_overlay )
                        }
                    } }
                />

                <div className={ 'enlightenment-header-overlay' + ( hasCustomOverlay ? '' : ' hidden' ) }>
                    <ColorPicker
                        color={ ( () => {
							var color = overlayColor || args.header_style.header_overlay.last_overlay

							if ( color.substring( 0, 5 )  == 'rgba(') {
								color = color.slice( 5 ).slice( 0, -1 ).split(',').map( ( element, index ) => {
									switch( index ) {
										case 0:
										case 1:
										case 2:
											element = parseInt( element.trim(), 10 )
											element = ( element | 1 << 8 ).toString( 16 ).slice( 1 )

											break

										case 3:
											element = parseFloat( element.trim() ) * 100
											element = ( ( 255 * element / 100 ) | 1 << 8 ).toString( 16 ).slice( 1 )

											break
									}

									return element
								} ).join( '' ).padStart( 9, '#' )
							}

                            return color
                        } )() }
                        onChangeComplete={ ( value ) => {
							if ( value.rgb.a == 1 ) {
								setOverlayColor( value.hex.substr( 0, 7 ) )
							} else {
								setOverlayColor( `rgba(${value.rgb.r},${value.rgb.g},${value.rgb.b},${value.rgb.a})` )
							}
                        } }
						enableAlpha
                    />
                </div>
            </div>
        </PluginDocumentSettingPanel>
    )
}

registerPlugin('enlightenment-header-style', {
    render: EnlightenmentHeaderStyle,
    icon: '',
})
