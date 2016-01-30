package codereview

import (
	"testing"
	"net/url"
	"github.com/stretchr/testify/assert"
)

func TestImages_post(t *testing.T) {
	c := NewClient()
	registerUser(c)
	imageId := "abcdef";
	res := c.post("images", url.Values{"link": {"https://avatars1.githubusercontent.com/u/15997353?v=3&s=200"},
		"imageId":{imageId}, "description": {"It's my avatar"}});
	assert.NotNil(t, res)
	image := c.getData("images/" + imageId, url.Values{})
	assert.NotNil(t, image["link"])
	assert.Equal(t, "It's my avatar", image["description"]);
}

func addImage(c *Client, imageId string) {
	c.post("images", url.Values{"link": {"https://avatars1.githubusercontent.com/u/15997353?v=3&s=200"},
		"imageId":{imageId}, "description": {"It's my avatar"}});
}

func TestImages_get(t *testing.T) {

}