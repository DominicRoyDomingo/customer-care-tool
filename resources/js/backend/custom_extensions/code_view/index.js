import { CodeView as OriginalCodeView } from "element-tiptap";
import CustomCodeViewButton from "./CodeViewButton";

export default class CodeView extends OriginalCodeView {
  // here you can extend the extension like tiptap, such as schema, plugins ...

  menuBtnView({ isActive, commands, focus, editor }) {
    return {
      component: CustomCodeViewButton,
      componentProps: {
      },
      componentEvents: {
        click: () => {
          console.log(editor);
          // you can do somethings when button clicked
          // .codeView();
        }
      }
    };
  }
}
