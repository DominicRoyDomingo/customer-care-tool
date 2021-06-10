export default{
	setQuestionList ( state, response ) {
		state.questionList = response
	},

	setCurrentQuestion ( state, response ) {
		state.currentQuestion = response
	},
	setChoices ( state, response ) {
		state.choices = response
	},
	setQuestionListItems ( state, response ) {
		state.questionListItems = response
	}
}