<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * ### Try ViewHelper
 *
 * Attempts to render child content. If an Exception is encountered
 * while rendering, instead the `f:else` child node is rendered,
 * if it is present. If `f:else` is not used, no output is returned.
 *
 * Can be used to perform complex translations of Exception messages
 * which can occur. Can naturally also be used to provide a great
 * deal of additional information about every possible Exception-type
 * error which Fluid can encounter (and there are many).
 *
 * Note that this is a Condition ViewHelper which means you can use
 * the `f:then` child node but it differs from regular Conditions by
 * also allowing the template developer to skip the `f:then` child
 * node and use the direct tag content as the "TRUE" condition and
 * add an `f:else` which is only rendered in case of an Exception
 * during rendering.
 *
 * Also note that you can use the `then` and `else` attributes; the
 * `then` attribute is what is attempted rendered and the `else`
 * attribute is what is rendered if retrieving the `then` attribute's
 * value fails. Which clearly only makes sense if for example complex
 * inline ViewHelpers are used in the attributes.
 *
 * ### Example usage
 *
 * #### Please note that this is a theoretical example!
 *
 * The example is theoretical in one major aspect: v:format.json.decode
 * throws an Exception which Fluid displays as a string always - abstract
 * from this and imagine that a plain Exception happens on errors.
 *
 *     <v:try>
 *         <!-- assume that the variable {badJson} contains the string "DontDecodeMe"
 *              which if course is invalid JSON and cannot be decoded. The default
 *              behavior is to simply output a simple "cannot decode" string. -->
 *         <v:variable.set name="decodedBadJson" value="{badJson -> v:format.json.decode()}" />
 *         Displayed only if the JSON decode worked. Much more code and many more
 *         ViewHelpers can go here. Now, imagine that this block spans so much code
 *         that potentially there could come an Exception from many additional places
 *         (for example from Widgets) and you cannot be sure where the Exception comes
 *         from but still want to tell the user what exactly went wrong and provide
 *         an error code which makes sense to send to developers if problems persist:
 *         <f:else>
 *             <h4>
 *                 Error in "{exception.trace.0.class
 *                     -> v:iterator.explode(glue: '_')
 *                     -> v:iterator.pop()
 *                     -> v:format.replace(substring: 'ViewHelper', replacement: ''}"
 *                 <small>{exception.code}</small>
 *                 <!-- Output example: "Error in Decode <small>1358440054</small>" -->
 *             </h4>
 *             <p>
 *                 {exception.message}
 *                 <!-- Output example: "The provided argument is invalid JSON" -->
 *             </p>
 *             <pre>
 *                 Value: ``{exception.trace.0.args.0}´´
 *                 <!-- Output example: "Value: ``DontDecodeMe´´" which is quite nice
 *                      for developers to know as part of a bug report from users. -->
 *             </pre>
 *         </f:else>
 *     </v:try>
 *     ...or if you want a shorter version...
 *     <!-- Tries to encode an object, if it fails, falls back by returning a proper JSON
 *          value, thus preventing breakage by the JSON consumer whatever it may be. -->
 *     {v:try(then: '{badObject -> v:format.json.encode()}', else: '{"validJson": "validValue"')}
 *     <!-- Note: be VERY careful about the inline JSON syntax! It's very close to Fluids. Always
 *          double quote your object variables' names, that prevents almost all issues! -->
 */
class TryViewHelper extends AbstractConditionViewHelper
{

    use TemplateVariableViewHelperTrait;

    /**
     * @return mixed
     */
    public function render()
    {
        try {
            $content = $this->renderThenChild();
            if (true === empty($content)) {
                $content = $this->renderChildren();
            }
        } catch (\Exception $error) {
            $content = $this->renderChildrenWithVariables(['exception' => $error]);
        }
        return $content;
    }
}
