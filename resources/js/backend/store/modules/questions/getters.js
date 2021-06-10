export default {
	questionList : state => {
		return state.questionList
	},
	questionById: (state) => (id) => {
		return state.questionListItems.find(question => question.id === id);
	},
	
	getCurrentQuestion : state => {
		return state.currentQuestion
	},

	getCurrentQuestionNameByLocale : (state) => (locale) => {
		return state.currentQuestion.translated_question[locale] ?? ""
	},

	getChoices : state => {
		return state.choices
	},
	questionListItems : state => {
		return state.questionListItems
	},
}