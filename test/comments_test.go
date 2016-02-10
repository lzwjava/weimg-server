package codereview

import (
	"testing"
	"net/url"
	"github.com/stretchr/testify/assert"
)

func TestComments_create(t *testing.T) {
	setUp()
	c := NewClient()
	postId := addImageAndPost(c)
	res := c.postData("posts/" + postId + "/comments", url.Values{"content": {"大惊小怪"}})
	assert.NotNil(t, res)
	assert.NotNil(t, res["commentId"])

	commentId := floatToStr(res["commentId"])
	res = c.postData("posts/" + postId + "/comments",
		url.Values{"content": {"呵呵"}, "parentId":{commentId}})
	assert.NotNil(t, res)
	assert.NotNil(t, res["commentId"])
}
